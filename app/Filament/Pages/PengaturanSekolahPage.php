<?php

namespace App\Filament\Pages;

use App\Models\PengaturanSekolah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class PengaturanSekolahPage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil & Rekening Bank';

    protected static ?string $title = 'Pengaturan Sekolah & Rekening Bank';

    protected static string $view = 'filament.pages.pengaturan-sekolah-page';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = PengaturanSekolah::first() ?? new PengaturanSekolah();
        $this->form->fill($setting->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Sekolah (Untuk Kop Surat Kwitansi)')->schema([
                    Forms\Components\TextInput::make('nama_sekolah')
                        ->label('Nama Resmi Sekolah')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('nomor_telepon')
                        ->label('Nomor Telepon / Kontak')
                        ->maxLength(50),

                    Forms\Components\TextInput::make('email_sekolah')
                        ->label('Email Resmi Sekolah')
                        ->email()
                        ->maxLength(100),

                    Forms\Components\Textarea::make('alamat_sekolah')
                        ->label('Alamat Lengkap Sekolah')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(3),

                Forms\Components\Section::make('Rekening Bank Tujuan Transfer (Untuk Pembayaran Siswa)')->schema([
                    Forms\Components\TextInput::make('nama_bank')
                        ->label('Nama Bank')
                        ->placeholder('Contoh: Bank Syariah Indonesia (BSI) / BRI')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('nomor_rekening')
                        ->label('Nomor Rekening Bank')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('atas_nama_rekening')
                        ->label('Atas Nama Rekening')
                        ->required()
                        ->maxLength(150),
                ])->columns(3),

                Forms\Components\Section::make('Penandatangan Kwitansi')->schema([
                    Forms\Components\TextInput::make('nama_bendahara')
                        ->label('Nama Bendahara Sekolah')
                        ->required()
                        ->maxLength(150),

                    Forms\Components\TextInput::make('nip_bendahara')
                        ->label('NIP / NBM Bendahara (Opsional)')
                        ->maxLength(50),
                ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $setting = PengaturanSekolah::first() ?? new PengaturanSekolah();
        $setting->fill($data);
        $setting->save();

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }
}
